/*
 * MIT License
 *
 * Copyright (c) 2018-present Marx J. Moura
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

import FileFactory from './file-factory'
import FileInfoWrapper from './file-info'
import FileReaderWrapper from './file-reader'
import FileSaveWrapper from './file-save'
import FileSizeWrapper from './file-size'
import FileWriterWrapper from './file-writer'

class FileWrapper {
  constructor (file) {
    this._file = new FileFactory(file).create()
    this._info = new FileInfoWrapper(this._file)
    this._reader = new FileReaderWrapper(this._file)
    this._save = new FileSaveWrapper(this._file)
    this._size = new FileSizeWrapper(this._file)
    this._writer = new FileWriterWrapper(this._file)
  }

  ext () {
    return this._info.ext()
  }

  fullName () {
    return this._info.fullName()
  }

  greaterThan (size, unit) {
    return this._size.greaterThan(size, unit)
  }

  greaterOrEqual (size, unit) {
    return this._size.greaterOrEqual(size, unit)
  }

  lowerThan (size, unit) {
    return this._size.lowerThan(size, unit)
  }

  lowerOrEqual (size, unit) {
    return this._size.lowerOrEqual(size, unit)
  }

  name () {
    return this._info.name()
  }

  readChunk (callback) {
    this._reader.readChunk(callback)
  }

  readLine (callback) {
    this._reader.readLine(callback)
  }

  save (name, type) {
    this._save.save(name, type)
  }

  size (unit) {
    return this._size.calculate(unit)
  }

  toFile () {
    return this._file
  }

  type () {
    return this._info.type()
  }

  write (content) {
    const file = this._writer.write(content)
    return io(file)
  }

  writeLine (content) {
    const file = this._writer.writeLine(content)
    return io(file)
  }
}

export default FileWrapper
